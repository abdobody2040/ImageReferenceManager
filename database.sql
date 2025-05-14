-- Database schema for PharmaEvents

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(120) UNIQUE NOT NULL,
    password_hash VARCHAR(256) NOT NULL,
    role VARCHAR(20) NOT NULL, -- 'admin', 'event_manager', or 'medical_rep'
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Application settings table
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
    venue_id INTEGER REFERENCES venue(id),
    service_request_id INTEGER REFERENCES service_request(id),
    employee_code_id INTEGER REFERENCES employee_code(id),
    event_type_id INTEGER REFERENCES event_type(id) NOT NULL,
    description TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id INTEGER REFERENCES users(id) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending' -- 'pending', 'approved', 'rejected'
);

-- Junction table for many-to-many relationship between events and categories
CREATE TABLE IF NOT EXISTS event_categories (
    event_id INTEGER REFERENCES event(id) ON DELETE CASCADE,
    category_id INTEGER REFERENCES event_category(id) ON DELETE CASCADE,
    PRIMARY KEY (event_id, category_id)
);

-- Create default admin user (password: admin123)
INSERT INTO users (email, password_hash, role)
VALUES ('admin@example.com', '$2y$10$Yl7nd6S9gOUxNVtZUWxnPeQvUlGDkQpPWi3JJZiCDhHQzDaU24vxK', 'admin')
ON CONFLICT (email) DO NOTHING;

-- Create default app settings
INSERT INTO app_setting (key, value)
VALUES 
    ('app_name', 'PharmaEvents'),
    ('theme', 'light')
ON CONFLICT (key) DO NOTHING;

-- Create default event categories
INSERT INTO event_category (name)
VALUES 
    ('Medical Conference'),
    ('Training Workshop'),
    ('Product Launch'),
    ('Symposium'),
    ('Seminar')
ON CONFLICT (name) DO NOTHING;

-- Create default event types
INSERT INTO event_type (name)
VALUES 
    ('In-Person'),
    ('Virtual'),
    ('Hybrid')
ON CONFLICT (name) DO NOTHING;

-- Create some sample venues
INSERT INTO venue (name, governorate)
VALUES 
    ('Fairmont Nile City', 'Cairo'),
    ('Four Seasons Hotel', 'Alexandria'),
    ('Steigenberger Hotel', 'Giza')
ON CONFLICT (id) DO NOTHING;

-- Create some sample service requests
INSERT INTO service_request (name)
VALUES 
    ('Catering'),
    ('AV Equipment'),
    ('Registration Desk'),
    ('Speaker Management')
ON CONFLICT (name) DO NOTHING;

-- Create some sample employee codes
INSERT INTO employee_code (code, name)
VALUES 
    ('EMP001', 'Dr. Ahmed Hassan'),
    ('EMP002', 'Dr. Sara Mahmoud'),
    ('EMP003', 'Dr. Mohamed Ali')
ON CONFLICT (code) DO NOTHING;