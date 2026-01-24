// server.js
require('dotenv').config();
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const axios = require('axios');

const app = express();
const server = http.createServer(app);
const io = new Server(server, {transports: ['websocket']});

const HOST = process.env.HOST || '0.0.0.0';
const PORT = process.env.PORT || 3000;
const laravelHost = process.env.LARAVEL_HOST;
const laravelPort = process.env.LARAVEL_PORT;
const laravelDomain = `http://${laravelHost}:${laravelPort}`;
const allowedOrigin = process.env.ALLOWED_ORIGIN || '*';

let messages = []; // Store messages temporarily

// Enable CORS for all routes
app.use(cors({
    origin: allowedOrigin, // Replace with the origin you want to allow
    methods: ['GET', 'POST'], // Specify methods if needed
    allowedHeaders: ['Content-Type', 'Authorization'],
    credentials: true, // Allow cookies to be sent if needed
}));

app.get('/', (req, res) => {
    res.send('Socket.io server is running');
});

io.on('connection', (socket) => {
    console.log('A user connected, socket id => ', socket.id);

    socket.on('set-present-match-data', (matchId) => {
        io.emit('set-updated-match-data', matchId);
        io.emit('set-scoreboard-match-data', matchId);
    });
    socket.on('update-match-score', (matchId) => {
        io.emit('set-match-score', matchId);
    });
    socket.on('set-match-status', (matchId) => {
        io.emit('get-match-status', matchId); 
    });
    socket.on('update-players', (data) => {
        io.emit('updated-players', data); 
    });
    socket.on('innings-completed', (data) => {
        io.emit('set-innings-completed', data)
    })
    socket.on('disconnect', () => {
        console.log('User disconnected');
    });
});

server.listen(PORT, HOST,() => {
    console.log(`Socket.io server is running on port http://${HOST}:${PORT}`);
});
