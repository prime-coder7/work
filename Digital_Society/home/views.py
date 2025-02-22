from django.shortcuts import render, redirect, HttpResponse
from django.contrib.auth import login, logout, authenticate
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from django.contrib.auth.models import User
from home.models import *
from django.contrib.auth.decorators import login_required
from django.http import HttpResponseForbidden
from django.http import JsonResponse
from django.utils import timezone


def admin_required(view_func):
    """
    Custom decorator to ensure that only users with 'admin' role can access the view.
    Redirect non-admin users to the user index page.
    """
    def _wrapped_view(request, *args, **kwargs):
        if not request.user.is_authenticated:
            return redirect('login_user')  # Redirect to login if not authenticated
        if request.user.role != 'admin':
            return redirect('index')  # Redirect to user index page if not an admin
        return view_func(request, *args, **kwargs)

    return login_required(_wrapped_view)  # Ensure login is required for all views using this decorator




# Create your views here.
@login_required(login_url="login_user")
@admin_required
def admin_dashboard(request):
    events = Event.objects.all()
    members = Member.objects.all()

    return render(request, 'admin/admin_dashboard.html', {'events': events, 'members': members})

@login_required(login_url="login_user")
def index(request):
    # Fetch the latest 5 events and notices for display
    events = Event.objects.all().order_by('event_date')[:5]
    notices = Notice.objects.all().order_by('-posted_at')[:5]
    
    return render(request, 'index.html', {
        'events': events,
        'notices': notices,
    })


def login_user(request):
    if request.user.is_authenticated:
        # If the user is already authenticated, check their role
        return redirect_based_on_role(request.user)  # Pass request.user instead of request

    if request.method == "POST":
        username = request.POST.get('username')
        password = request.POST.get('password')

        if not username or not password:
            messages.error(request, "Enter All Details")
            return redirect("login_user")
        else:
            user = authenticate(username=username, password=password)

            if user:
                login(request, user)
                # After login, check the role of the user
                return redirect_based_on_role(user)  # Pass user instead of request
            else:
                messages.error(request, "Invalid Username or Password")
                return redirect("login_user")
    
    return render(request, "login.html")

def redirect_based_on_role(user):
    if user.is_authenticated:
        if user.role == 'admin':  # Check if the user is an admin
            return redirect('admin_dashboard')  # Redirect to the admin dashboard
        else:
            return redirect('index')  # Redirect to the user index page
    else:
        return redirect('login_user')  # If the user is not authenticated, redirect to the login page


def signup_user(request):
    if request.user.is_authenticated:
        return redirect("index")
    if request.method == "POST":
        data = request.POST
        username = data.get('username')
        email = data.get('email')
        password = data.get('password')
        cpassword = data.get('cpassword')

        if not username or not email or not password or not cpassword:
            messages.error(request, "Enter All The Details")
            return render(request, "signup.html")
        else:
            if password == cpassword:
                user = User(username=username, email=email)
                user.set_password(password)
                user.save()

                # Create the corresponding Member instance with role from User
                member = Member(user=user, status='active', role=user.role)  # Set role from User model
                member.save()

                messages.success(request, "Registration Successfull")
                return redirect("login_user")
            else:
                messages.error(request, "Please Enter Same Password")
                return render(request, "signup.html") 
    return render(request, "signup.html")


def logout_user(request):
    if request.user.is_authenticated:
        logout(request)
        messages.success(request, "Logout Succesfull")
    return render(request, "login.html")

from django.contrib.auth.forms import PasswordChangeForm
from django.contrib.auth import update_session_auth_hash

@login_required
def pass_change(request):
    if request.method == 'POST':
        form = PasswordChangeForm(user=request.user, data=request.POST)
        if form.is_valid():
            # Save the new password
            form.save()
            # Keep the user logged in after changing the password
            update_session_auth_hash(request, form.user)
            # Show a success message
            messages.success(request, 'Your password was successfully updated!')
            # Redirect to the success page
            return redirect('password_change_done')  # You can customize this URL
        else:
            # If the form is invalid, show an error message
            messages.error(request, 'Please correct the errors below.')
    else:
        # If GET request, show the form
        form = PasswordChangeForm(user=request.user)
    
    # Render the password change form
    return render(request, 'admin/pass_change.html', {'form': form})

def pass_change_done(request):
    return render(request, 'admin/pass_change_done.html')

@admin_required
def admin_profile(request):
    return render(request, "admin/admin_profile.html")

@admin_required
def manage_members(request):
    return render(request, "admin/members.html")

@admin_required
def manage_watchmens(request):
    return render(request, "admin/watchmens.html")

@admin_required
def manage_notice(request):
    notices = Notice.objects.all()
    return render(request, "admin/notice.html", {'notices': notices})


def add_notice(request):
    if request.method == 'POST':
        title = request.POST.get('title')
        content = request.POST.get('content')

        try:
            # Try to fetch the Member instance associated with the logged-in user
            member = Member.objects.get(user=request.user)
        except Member.DoesNotExist:
            # If no Member exists, create a new one and set the role from User
            member = Member(user=request.user, status='active', role=request.user.role)  # Copy role from User
            member.save()

        # Create and save the notice
        notice = Notice(
            title=title,
            content=content,
            posted_by=member,
            posted_at=timezone.now()
        )
        notice.save()

        return JsonResponse({
            'status': 'success',
            'message': 'Notice posted successfully!',
            'title': notice.title,
            'content': notice.content,
            'posted_by': f"{notice.posted_by.user.first_name} {notice.posted_by.user.last_name}",
            'posted_at': notice.posted_at.strftime('%Y-%m-%d %H:%M:%S')
        })
    else:
        return JsonResponse({'status': 'error', 'message': 'Invalid request method.'})


def notice_view(request):
    notices = Notice.objects.all()
    return render(request, 'admin/notice.html', {'notices': notices})



@admin_required
def manage_events(request):
    return render(request, "admin/events.html")






from django.shortcuts import render
from django.contrib.auth import logout
from django.shortcuts import redirect

def user_dashboard(request):
    return render(request, 'user_dashboard.html')

def user_profile(request):
    return render(request, 'user_profile.html')

def user_events(request):
    return render(request, 'user_events.html')

def user_notices(request):
    return render(request, 'user_notices.html')

def user_members(request):
    return render(request, 'user_members.html')

def user_watchmen(request):
    return render(request, 'user_watchmen.html')

def logout_view(request):
    logout(request)
    return redirect('login')  # Redirect to login after logout
