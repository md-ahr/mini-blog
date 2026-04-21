-- Demo dashboard user (no registration flow). Password: 12345678
-- Run after 002_blog_schema_relations.sql (needs role, status columns).

INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `status`)
VALUES (
  'Owner',
  'ahr@gmail.com',
  '$2y$12$qVME6bT/l6.8yJTXAzsIJ.t6vD4XJRLsOvhnclwo81Z8A/7r91mji',
  'owner',
  'active'
)
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `password_hash` = VALUES(`password_hash`),
  `role` = VALUES(`role`),
  `status` = VALUES(`status`);
