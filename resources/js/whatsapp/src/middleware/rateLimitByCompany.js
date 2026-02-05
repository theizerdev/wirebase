const rateLimit = require('express-rate-limit');

const rateLimitByCompany = (req, res, next) => {
  const company = req.company;
  
  if (!company) {
    return next();
  }

  const limiter = rateLimit({
    windowMs: 60 * 1000, // 1 minuto
    max: company.rateLimitPerMinute,
    keyGenerator: (req) => `company_${company.id}`,
    message: {
      success: false,
      error: `Límite de ${company.rateLimitPerMinute} mensajes por minuto excedido`
    },
    standardHeaders: true,
    legacyHeaders: false
  });

  limiter(req, res, next);
};

module.exports = rateLimitByCompany;