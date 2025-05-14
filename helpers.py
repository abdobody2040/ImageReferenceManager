import re
from functools import wraps
from flask import flash, redirect, url_for
from flask_login import current_user

# Allowed file extensions for upload
ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg'}

def allowed_file(filename):
    """Check if a filename has an allowed extension"""
    return '.' in filename and \
           filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

def admin_required(f):
    """Decorator to require admin role for a route"""
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not current_user.is_admin():
            flash('This action requires administrator privileges', 'danger')
            return redirect(url_for('dashboard'))
        return f(*args, **kwargs)
    return decorated_function

def get_governorates():
    """Return list of governorates in Egypt"""
    return [
        'Alexandria', 'Aswan', 'Asyut', 'Beheira', 'Beni Suef', 
        'Cairo', 'Dakahlia', 'Damietta', 'Faiyum', 'Gharbia', 
        'Giza', 'Ismailia', 'Kafr El Sheikh', 'Luxor', 'Matruh', 
        'Minya', 'Monufia', 'New Valley', 'North Sinai', 'Port Said', 
        'Qalyubia', 'Qena', 'Red Sea', 'Sharqia', 'Sohag', 
        'South Sinai', 'Suez'
    ]

def validate_email(email):
    """Validate email format"""
    pattern = r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
    return re.match(pattern, email) is not None

def format_datetime(dt, format='%d %b %Y, %H:%M'):
    """Format datetime object to string"""
    if dt:
        return dt.strftime(format)
    return ''

def get_event_badge_class(is_online):
    """Return appropriate badge class based on event type"""
    return "bg-info" if is_online else "bg-success"
