-- Add votes_required column to positions table
ALTER TABLE `positions` ADD COLUMN `votes_required` INT NOT NULL DEFAULT 1 AFTER `description`;

-- Update existing positions
UPDATE `positions` SET `votes_required` = 1 WHERE `votes_required` IS NULL;
