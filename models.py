from datetime import datetime
from app import db
from flask_login import UserMixin
from werkzeug.security import generate_password_hash, check_password_hash

# Many-to-many relationship for events and categories
event_categories = db.Table('event_categories',
    db.Column('event_id', db.Integer, db.ForeignKey('event.id'), primary_key=True),
    db.Column('category_id', db.Integer, db.ForeignKey('event_category.id'), primary_key=True)
)

class User(UserMixin, db.Model):
    id = db.Column(db.Integer, primary_key=True)
    email = db.Column(db.String(120), unique=True, nullable=False)
    password_hash = db.Column(db.String(256), nullable=False)
    role = db.Column(db.String(20), nullable=False)  # 'admin', 'event_manager', or 'medical_rep'
    created_at = db.Column(db.DateTime, nullable=False, default=datetime.utcnow)
    
    events = db.relationship('Event', backref='creator', lazy=True)
    
    def set_password(self, password):
        self.password_hash = generate_password_hash(password)
        
    def check_password(self, password):
        return check_password_hash(self.password_hash, password)
    
    def is_admin(self):
        return self.role == 'admin'
        
    def is_medical_rep(self):
        return self.role == 'medical_rep'


class AppSetting(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    key = db.Column(db.String(50), unique=True, nullable=False)
    value = db.Column(db.Text, nullable=True)
    
    def __repr__(self):
        return f'<AppSetting {self.key}>'

class EventCategory(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(50), unique=True, nullable=False)
    
    def __repr__(self):
        return f'<EventCategory {self.name}>'


class EventType(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(50), unique=True, nullable=False)
    
    events = db.relationship('Event', backref='event_type', lazy=True)
    
    def __repr__(self):
        return f'<EventType {self.name}>'


class Venue(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    governorate = db.Column(db.String(50), nullable=False)
    
    events = db.relationship('Event', backref='venue_details', lazy=True)
    
    def __repr__(self):
        return f'<Venue {self.name}, {self.governorate}>'


class ServiceRequest(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), unique=True, nullable=False)
    
    events = db.relationship('Event', backref='service_request', lazy=True)
    
    def __repr__(self):
        return f'<ServiceRequest {self.name}>'


class EmployeeCode(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    code = db.Column(db.String(20), unique=True, nullable=False)
    name = db.Column(db.String(100), nullable=False)
    
    events = db.relationship('Event', backref='employee', lazy=True)
    
    def __repr__(self):
        return f'<EmployeeCode {self.code} - {self.name}>'


class Event(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    requester_name = db.Column(db.String(100), nullable=False)
    is_online = db.Column(db.Boolean, default=False)
    image_url = db.Column(db.String(255), nullable=True)
    image_file = db.Column(db.String(255), nullable=True)
    start_datetime = db.Column(db.DateTime, nullable=False)
    end_datetime = db.Column(db.DateTime, nullable=False)
    registration_deadline = db.Column(db.DateTime, nullable=False)
    governorate = db.Column(db.String(50), nullable=True)
    venue_id = db.Column(db.Integer, db.ForeignKey('venue.id'), nullable=True)
    service_request_id = db.Column(db.Integer, db.ForeignKey('service_request.id'), nullable=True)
    employee_code_id = db.Column(db.Integer, db.ForeignKey('employee_code.id'), nullable=True)
    event_type_id = db.Column(db.Integer, db.ForeignKey('event_type.id'), nullable=False)
    description = db.Column(db.Text, nullable=True)
    attendees_file = db.Column(db.String(255), nullable=False)  # Store filename of uploaded attendees list
    created_at = db.Column(db.DateTime, nullable=False, default=datetime.utcnow)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    status = db.Column(db.String(20), nullable=False, default='pending')  # 'pending', 'approved', 'rejected'
    
    # Many-to-many relationship with categories
    categories = db.relationship('EventCategory', secondary=event_categories,
        lazy='subquery', backref=db.backref('events', lazy=True))
    
    def __repr__(self):
        return f'<Event {self.name}>'
    
    def get_status(self):
        return "Online" if self.is_online else "Offline"
        
    def get_approval_status(self):
        return self.status
        
    def is_approved(self):
        return self.status == 'approved'
        
    def is_pending(self):
        return self.status == 'pending'
        
    def is_rejected(self):
        return self.status == 'rejected'
