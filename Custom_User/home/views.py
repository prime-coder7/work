from django.shortcuts import render, redirect
from django.contrib.auth.decorators import login_required
from django.contrib.auth import login, logout, authenticate
from home.models import *

# Create your views here.
login_required(login_url="login_user")
def index(request):
    if request.user.is_anonymous:
        return redirect("login_user")
    return render(request, 'index.html')

def login_user(request):
    if request.user.is_authenticated:
        return redirect("index")
    if request.method ==  "POST":
        email = request.POST.get('email')
        password = request.POST.get('pass')
        
        if not email or not password :
            return render(request, 'login.html', {'error': 'Please enter both email and password'})
        else:
            user = authenticate(email=email, password=password)
            if user is not None:
                login(request, user)
                return redirect('index')
            
    return render(request, 'login.html')

def signup_user(request):
    if request.user.is_authenticated:
        return redirect("index")
    
    if request.method == "POST":
        fname = request.POST.get('fname')
        email = request.POST.get('email')
        password = request.POST.get('pass')
        
        if not email or not password or not fname:
            return render(request, 'signup.html', {'error': 'Please enter all fields'})
        else:
            user = CustomUSer.objects.create_user(email=email, first_name=fname, password=password)
            user.set_password(password)
            user.save()
            return redirect('login_user')
    return render(request, 'signup.html')

def logout_user(request):
    if request.user.is_authenticated:
        logout(request)
    return render(request, 'login.html')