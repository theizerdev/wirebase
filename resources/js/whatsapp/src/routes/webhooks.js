const express = require('express');
const router = express.Router();

router.post('/', (req, res) => {
  console.log('Webhook received:', req.body);
  res.json({ success: true });
});

module.exports = router;