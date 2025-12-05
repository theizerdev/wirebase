const express = require('express');
const Message = require('../models/Message');
const router = express.Router();

router.get('/', async (req, res) => {
  try {
    const total = await Message.count();
    const sent = await Message.count({ where: { status: 'sent' } });
    const failed = await Message.count({ where: { status: 'failed' } });
    
    res.json({
      success: true,
      stats: { total, sent, failed }
    });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

module.exports = router;