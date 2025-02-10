from django.shortcuts import render
from django.contrib.auth.decorators import login_required

# Create your views here.
@login_required(login_url="login_user")
def index(request):
    return render(request, "index.html")

def login_user(request):
    return render(request, "login.html")

def forgot_password(request):
    return render(request, "login.html")

def signup_user(request):
    return render(request, "signup.html")

def logout_user(request):
    return render(request, "login.html")