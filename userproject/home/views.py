from django.shortcuts import render, redirect
from home.models import *

# Create your views here.
def index(request):
    if request.user.is_anonymous:
        return redirect("/login")
    return render(request, "index.html")

def customer(request):
    pass
    

def contact(request):
    pass
    

def login_user(request):
    return render(request, "login.html")

def logout_user(request):
    return redirect("/login")
    