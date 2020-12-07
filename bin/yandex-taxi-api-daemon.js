const http = require('http');
const https = require('https');

// Settings
const args = process.argv.slice(2);
let port = 3000;

// Global variables to work
let queue = [];
let executed = [];

if (args.length > 0 && args[0].startsWith('--port=')) {
    port = parseInt(args[0].replace('--port=', ''));
}

worker();

// control of the request queue
function worker() {
    // deleting obsolete data
    executed = executed.filter(obj => obj.executedAt + 1000 > Date.now());

    // running available requests
    for (let q in queue) {
        // max 2 request in 1 sec. for one client
        if (queue.hasOwnProperty(q) && executed.filter(obj => obj.client === queue[q].client).length < 2) {
            // remember for control API restrictions
            executed.push({
                client: queue[q].client,
                executedAt: Date.now(),
            });
            // execute request
            queue[q].callback();
            // exclude repeated request
            queue[q] = null;
        }
    }

    // clear queue
    queue = queue.filter(obj => obj);
    setTimeout(worker, 300);
}

// proxy server
http.createServer((req, res) => {
    let postData = '';

    req.on('data', chunk => {
        postData += chunk;
    });

    req.on('end', _ => {
        // for a test, send request without body
        if (postData === '') {
            res.end('Yandex daemon is working!');
            return;
        }

        queue.push({
            client: req.headers['x-client-id'],
            callback: function() {
                executeRequest(req, postData).then(
                    result => {
                        res.writeHead(200, {
                            'Content-Type': 'application/json',
                        });
                        res.end(result);
                    },
                    error => {
                        res.writeHead(400, {
                            'Content-Type': 'application/json',
                        });
                        res.end(JSON.stringify({ error: error }));
                    }
                );
            }
        });
    });
}).listen(port);

console.log(`Yandex daemon is running on port ${port}`);

function executeRequest(req, json) {
    return new Promise((resolve, reject) => {
        const options = {
            port: 443,
            method: 'POST',
            hostname: 'fleet-api.taxi.yandex.net',
            path: req.url,
            headers: {
                'Content-Type': 'application/json',
                'X-Client-ID': req.headers['x-client-id'],
                'X-API-Key': req.headers['x-api-key'],
                'Content-Length': json.length
            },
        };

        const request = https.request(options, res => {
            let content = '';

            res.on('data', chunk => {
                content += chunk;
            });
            res.on('end', _ => {
                resolve(content);
            });
        });

        request.on('error', e => {
            reject(e);
        });

        request.end(json);
    });
}
