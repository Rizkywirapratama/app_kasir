const express = require('express');
const app = express();
const http = require('http');
const server = http.createServer(app);
const { Server } = require("socket.io");
const io = new Server(server, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"]
  }
});

app.use(express.json());

// Endpoint untuk PHP mengirim notifikasi ke Node.js
app.post('/notify', (req, res) => {
  const { event, data } = req.body;
  console.log(`[EVENT] ${event}:`, data);
  io.emit(event, data);
  res.json({ status: 'sent' });
});

io.on('connection', (socket) => {
  console.log('A user connected');
  socket.on('disconnect', () => {
    console.log('User disconnected');
  });
});

const PORT = 3000;
server.listen(PORT, () => {
  console.log(`NexaPOS Bridge running on http://localhost:${PORT}`);
});
