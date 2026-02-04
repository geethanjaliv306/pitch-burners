require('dotenv').config();
const express = require('express');
const fs = require('fs');
const https = require('https');
const { Server } = require('socket.io');
const cors = require('cors');

const app = express();

// Create HTTPS server using the Let's Encrypt certificates
const server = https.createServer({
        cert: fs.readFileSync('/etc/letsencrypt/live/pitchburners-socket.upskilllabs.in/fullchain.pem'),
        key: fs.readFileSync('/etc/letsencrypt/live/pitchburners-socket.upskilllabs.in/privkey.pem')
}, app);

// Initialize socket.io with CORS settings to allow specific origins
const io = new Server(server, {
    cors: {
        origin: "*", // Use your local Laravel domain here
        methods: ["GET", "POST"],
        allowedHeaders: ["Content-Type", "Authorization"],
        credentials: true
    }
});

const HOST = process.env.HOST || '0.0.0.0';
const PORT = process.env.PORT || 3000;

// Middleware for enabling CORS globally in Express
app.use(cors({
    origin: "*", // Use your local Laravel domain here
    methods: ['GET', 'POST'],
    allowedHeaders: ['Content-Type', 'Authorization'],
    credentials: true,
}));

// Basic route for testing server status
app.get('/', (req, res) => {
    res.send('Secure Socket.io server is running');
});

// Socket.io connection events
io.on('connection', (socket) => {
    console.log('A user connected, socket id => ', socket.id);

    socket.on('set-present-match-data', (matchId) => {
        io.emit(`set-updated-match-data-${matchId}`, matchId);
        io.emit(`set-scoreboard-match-data-${matchId}`, matchId);
    });
    socket.on('update-match-score', (matchId) => {
        io.emit(`set-match-score-${matchId}`, matchId);
    });
    socket.on('set-match-status', (matchId) => {
        io.emit(`get-match-status-${matchId}`, matchId); 
    });
    socket.on('update-players', (data) => {
        io.emit(`updated-players-${data.match_id}`, data); 
    });
    socket.on('innings-completed', (data) => {
        io.emit(`set-innings-completed-${data.matchId}`, data)
    })
    socket.on('undo-last-data', (matchId) => {
        const timeOut = 1500;
        setTimeout(() => {
            io.emit(`undo-live-match-${matchId}`, matchId)
            io.emit(`undo-score-board-${matchId}`, matchId)
            io.emit(`undo-live-score-${matchId}`, matchId)
        }, timeOut)
    })
    socket.on('join-match-center', (scheduleMatchId) => {
        socket.join(scheduleMatchId);

        getUserCount(scheduleMatchId);
    });
    socket.on('get-live-user', (data) => {
        let count = data.map(scheduleMatchId => {
            const userCount = io.sockets.adapter.rooms.get(scheduleMatchId)?.size || 0;
            return {scheduleMatchId, userCount}
        });
        io.emit('set-live-user', count);
    })
    socket.on('leave-match', (scheduleMatchId) => {
        socket.leave(scheduleMatchId);

        getUserCount(scheduleMatchId);
    })
    socket.on('disconnect', () => {
        console.log('User disconnected');
    });
});
function getUserCount(scheduleMatchId){
    const userCount = io.sockets.adapter.rooms.get(scheduleMatchId)?.size || 0;
    io.emit(`live-user-count-${scheduleMatchId}`, {scheduleMatchId, userCount});
}
// Start the HTTPS server
server.listen(PORT, HOST, () => {
    console.log(`Secure Socket.io server is running on https://pitchburners.com:${PORT}`);
});