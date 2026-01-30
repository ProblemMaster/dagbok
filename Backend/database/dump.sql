-- Schema for activities
CREATE TABLE activities (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
);

-- Schema for workouts
CREATE TABLE workouts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  activity_id INTEGER NOT NULL,
  date DATE NOT NULL,
  description TEXT,
  effort_level INTEGER NOT NULL,
  distance_value DECIMAL(8,2),
  distance_unit VARCHAR(10),
  duration_value INTEGER,
  duration_unit VARCHAR(10),
  image_path VARCHAR(255),
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
);

-- Seed activities
INSERT INTO activities (id, name, created_at, updated_at) VALUES
(1, 'Löpning', datetime('now'), datetime('now')),
(2, 'Cykling', datetime('now'), datetime('now')),
(3, 'Yoga', datetime('now'), datetime('now'));

-- Seed workouts
INSERT INTO workouts (activity_id, date, description, effort_level, distance_value, distance_unit, duration_value, duration_unit, image_path, created_at, updated_at) VALUES
(1, '2026-01-01', 'Kort löprunda', 5, 5.20, 'km', 30, 'min', NULL, datetime('now'), datetime('now')),
(1, '2026-01-10', 'Längre löpning', 7, 12.50, 'km', 70, 'min', NULL, datetime('now'), datetime('now')),
(2, '2026-01-05', 'Lätt cykling', 4, 10.00, 'km', 40, 'min', NULL, datetime('now'), datetime('now'));
