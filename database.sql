-- PharmaEvents - Database Schema
-- PostgreSQL Schema

-- Drop tables if they exist (for clean reinstall)
DROP TABLE IF EXISTS event_category;
DROP TABLE IF EXISTS event;
DROP TABLE IF EXISTS employee_code;
DROP TABLE IF EXISTS service_request;
DROP TABLE IF EXISTS venue;
DROP TABLE IF EXISTS event_type;
DROP TABLE IF EXISTS event_category;
DROP TABLE IF EXISTS app_setting;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(120) UNIQUE NOT NULL,
    password_hash VARCHAR(256) NOT NULL,
    role VARCHAR(20) NOT NULL, -- 'admin', 'event_manager', or 'medical_rep'
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- App settings table
CREATE TABLE IF NOT EXISTS app_setting (
    id SERIAL PRIMARY KEY,
    key VARCHAR(50) UNIQUE NOT NULL,
    value TEXT
);

-- Event categories table
CREATE TABLE IF NOT EXISTS event_category (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

-- Event types table
CREATE TABLE IF NOT EXISTS event_type (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

-- Venues table
CREATE TABLE IF NOT EXISTS venue (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    governorate VARCHAR(50) NOT NULL
);

-- Service requests table
CREATE TABLE IF NOT EXISTS service_request (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

-- Employee codes table
CREATE TABLE IF NOT EXISTS employee_code (
    id SERIAL PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL
);

-- Events table
CREATE TABLE IF NOT EXISTS event (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    requester_name VARCHAR(100) NOT NULL,
    is_online BOOLEAN DEFAULT FALSE,
    image_url VARCHAR(255),
    image_file VARCHAR(255),
    start_datetime TIMESTAMP NOT NULL,
    end_datetime TIMESTAMP NOT NULL,
    registration_deadline TIMESTAMP NOT NULL,
    governorate VARCHAR(50),
    venue_id INTEGER REFERENCES venue(id) ON DELETE SET NULL,
    service_request_id INTEGER REFERENCES service_request(id) ON DELETE SET NULL,
    employee_code_id INTEGER REFERENCES employee_code(id) ON DELETE SET NULL,
    event_type_id INTEGER NOT NULL REFERENCES event_type(id) ON DELETE RESTRICT,
    description TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    status VARCHAR(20) NOT NULL DEFAULT 'pending' -- 'pending', 'approved', 'rejected'
);

-- Junction table for many-to-many relationship between events and categories
CREATE TABLE IF NOT EXISTS event_category_junction (
    event_id INTEGER REFERENCES event(id) ON DELETE CASCADE,
    category_id INTEGER REFERENCES event_category(id) ON DELETE CASCADE,
    PRIMARY KEY (event_id, category_id)
);

-- Default app settings
INSERT INTO app_setting (key, value) VALUES 
('app_name', 'PharmaEvents'),
('theme', 'light'),
('logo', '/static/img/logo.png');

-- Default admin user (password: admin123)
INSERT INTO users (email, password_hash, role) VALUES 
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Default event categories
INSERT INTO event_category (name) VALUES 
('Medical Conference'),
('Workshop'),
('Seminar'),
('Webinar'),
('Product Launch'),
('Training'),
('Symposium');

-- Default event types
INSERT INTO event_type (name) VALUES 
('Promotional'),
('Scientific'),
('Educational'),
('Corporate');

-- Default venues
INSERT INTO venue (name, governorate) VALUES 
('Cairo International Convention Center', 'Cairo'),
('Bibliotheca Alexandrina', 'Alexandria'),
('Semiramis InterContinental', 'Cairo'),
('Grand Nile Tower', 'Cairo'),
('Four Seasons Hotel', 'Alexandria');

-- Default service requests
INSERT INTO service_request (name) VALUES 
('Catering'),
('Audio/Visual Equipment'),
('Photography/Videography'),
('Interpretation Services'),
('Transportation'),
('Booth Setup');

-- Default employee codes
INSERT INTO employee_code (code, name) VALUES 
('EMP001', 'John Smith'),
('EMP002', 'Sarah Johnson'),
('EMP003', 'Mohammed Ali'),
('EMP004', 'Leila Ahmed'),
('EMP005', 'Omar Hassan');

-- Create indexes for better performance
CREATE INDEX idx_event_user_id ON event(user_id);
CREATE INDEX idx_event_status ON event(status);
CREATE INDEX idx_event_type_id ON event(event_type_id);
CREATE INDEX idx_event_venue_id ON event(venue_id);
CREATE INDEX idx_event_start ON event(start_datetime);
CREATE INDEX idx_venue_governorate ON venue(governorate);