from django.shortcuts import render, redirect
from django.contrib.auth import  authenticate, login, logout
from django.contrib import messages
from django.contrib.auth.models import User
from django.contrib.auth.decorators import login_required
from home.models import *

# Create your views here.

def index(request):
    return render(request, "index.html")

def login_user(request):
    if request.user.is_authenticated:
        return redirect("index")
    if request.method == "POST":
        data = request.POST
        username = data.get('uname')
        password = data.get('pass') 
        
        if not username or not password:
            messages.error(request, "Fill All The Details.")
            return render(request, "login.html")
        else:
            user = authenticate(username = username, password=password)
            if user:
                login(request , user)
                messages.success(request, "Login Successfull")
                return redirect("index")
            else:   
                messages.error(request, "Enter Correct Details")  
                return render(request, "login.html")
    return render(request, "login.html")

def logout_user(request):
    if request.user.is_anonymous:
        return redirect("login_user")
    logout(request)
    messages.success(request, "Logout Successfull")
    return render(request, "login.html")

def signup_user(request):
    if request.user.is_authenticated:
        return redirect("index")
    
    if request.method == "POST":
        data = request.POST
        username = data.get('uname')
        email = data.get('email')
        password = data.get('pass')
        
        if not username or not email or not password:
            messages.error(request, "Fill All The Details.")
            return render(request, "signup.html")
        else:
            user = User(username=username, email=email)
            user.set_password(password)
            user.save()
            messages.success(request, "Registration Successful")
            return redirect("login_user")
    return render(request, "signup.html")

def about(request):
    return render(request, "about.html")

def contact(request):
    return render(request, "contact.html")

@login_required(login_url="login_user")
def userform(request):
    allusers = MyUser.objects.all()
    return render(request, "userform.html", {"users":allusers})

def adduser(request):
    if request.method == "POST":
        data = request.POST
        id = data.get('id')
        username = data.get('uname')
        email = data.get('email')
        phone = data.get('phone')
        
        if not username or not email or not phone:
            messages.error(request, "Fill All The Details")
        else:
            if (id):
                cuser = MyUser.objects.get(pk=id)
                cuser.username = username
                cuser.email = email
                cuser.phone = phone
                cuser.save()
                messages.success(request, "User Updated Successfully")
            else:
                MyUser.objects.create(username=username, email=email, phone=phone) 
                messages.success(request, "User Created Successfully")   
    return render(request, "userform.html")

def edituser(request, id):
    pass

def deleteuser(request, id):
    pass

