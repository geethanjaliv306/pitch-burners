require('dotenv').config();
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const app = express();

// Create HTTP server (NO SSL for local)
const server = http.createServer(app);

// Initialize socket.io with CORS settings
const io = new Server(server, {
    cors: {
        origin: "*", // For local dev
        methods: ["GET", "POST"],
        allowedHeaders: ["Content-Type", "Authorization"],
        credentials: true
    }
});

const HOST = process.env.HOST || '0.0.0.0';
const PORT = process.env.PORT || 3000;

// Enable CORS for Express
app.use(cors({
    origin: "*",
    methods: ['GET', 'POST'],
    allowedHeaders: ['Content-Type', 'Authorization'],
    credentials: true,
}));

// Test route
app.get('/', (req, res) => {
    res.send('Local Socket.io HTTP server is running');
});

// Socket.io events
io.on('connection', (socket) => {
    console.log('User connected =>', socket.id);

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
        io.emit(`set-innings-completed-${data.matchId}`, data);
    });

    socket.on('undo-last-data', (matchId) => {
        setTimeout(() => {
            io.emit(`undo-live-match-${matchId}`, matchId);
            io.emit(`undo-score-board-${matchId}`, matchId);
            io.emit(`undo-live-score-${matchId}`, matchId);
        }, 1500);
    });

    socket.on('join-match-center', (scheduleMatchId) => {
        socket.join(scheduleMatchId);
        getUserCount(scheduleMatchId);
    });

    socket.on('get-live-user', (data) => {
        const count = data.map(scheduleMatchId => {
            const userCount = io.sockets.adapter.rooms.get(scheduleMatchId)?.size || 0;
            return { scheduleMatchId, userCount };
        });
        io.emit('set-live-user', count);
    });

    socket.on('leave-match', (scheduleMatchId) => {
        socket.leave(scheduleMatchId);
        getUserCount(scheduleMatchId);
    });

    socket.on('disconnect', () => {
        console.log('User disconnected');
    });
});

// User count helper
function getUserCount(scheduleMatchId) {
    const userCount = io.sockets.adapter.rooms.get(scheduleMatchId)?.size || 0;
    io.emit(`live-user-count-${scheduleMatchId}`, {
        scheduleMatchId,
        userCount
    });
}

// Start HTTP server
server.listen(PORT, HOST, () => {
    console.log(`Local Socket.io server running at http://localhost:${PORT}`);
});