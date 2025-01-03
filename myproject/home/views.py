from django.shortcuts import render, redirect
from django.contrib.auth import authenticate, login, logout
from django.contrib import messages 
from home.models import *


# Create your views here.
def index(request):
    if request.user.is_anonymous:
        return redirect("/login")
    return render(request, 'index.html')

def login_user(request):
    if request.method == "POST":
        username = request.POST.get("username")
        password = request.POST.get("password")
        
        user = authenticate(request, username=username, password=password)
        
        if user is not None:
            login(request, user)
            return redirect("/")
        else:
            messages.error(request, "Invalid username or password.")
            return redirect("/login")        
        
    return render(request, 'login.html')

def logout_user(request):
    logout(request)
    return redirect('/login')

def contact(request):
   if request.user.is_anonymous:
       return redirect("/login")
   if request.method == "POST":
       name=request.POST.get("name")
       email=request.POST.get("email")
       phone=request.POST.get("phone")
       desc=request.POST.get("desc")
       
       if not name or not email or not phone or not desc:
           messages.error(request, "Fill All Details")
       else:
           contact = Contact(name=name, email=email, phone=phone, desc=desc)
           contact.save()
           messages.success(request, "Your details submitted successfully")
           
   return render(request, "contact.html")