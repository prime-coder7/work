from django.shortcuts import render, redirect
from django.contrib.auth import authenticate, login, logout
from django.contrib import messages
from django.contrib.auth.models import User
from home.models import *

# Create your views here.
def index(request):
    return render(request, "index.html")

def loginuser(request):
    if request.user.is_authenticated:
        return redirect("index")
    
    if request.method == "POST":
        username = request.POST.get('username')
        password = request.POST.get('password')
        
        if not username or not password:
            messages.error(request, "Fill All Details")
        else:
            user = authenticate(request, username=username, password=password)
            
            if user:
                login(request, user)
                messages.success(request, "Login Successfull")
                return redirect("index")
            else:
                messages.error(request, "Enter Correct Details")
                return redirect("login")        
    return render(request, "login.html")

def logoutuser(request):
    logout(request)
    return redirect("login")

def signup(request):
    if request.user.is_authenticated:
        return redirect("index")
    
    if request.method == "POST":
        username = request.POST.get('uname')
        firstname = request.POST.get('fname')
        lastname = request.POST.get('lname')
        email = request.POST.get('email')
        password = request.POST.get('pass')
        
        if not username or not firstname or not lastname or not email or not password:
            messages.error(request, "Fill All Details")
        else:
            user = User.objects.create( username=username, first_name=firstname, last_name=lastname, email=email)
            user.set_password(password)
            user.save()
            messages.success(request, "User Created Successfully")
            return redirect("login")
    return render(request, "signup.html")

def userform(request):
    allusers = MyUSer.objects.all()
    return render(request, "userform.html", {"users":allusers})

def adduser(request):
    allusers = MyUSer.objects.all()
    
    if request.method == "POST":
        id = request.POST.get("id")
        name = request.POST.get('name')
        email = request.POST.get('email')
        phone = request.POST.get('phone')
        
        if not name or not email or not phone:
            messages.error(request, "Fill Alll Details")
        else:
            if id:
                u = MyUSer.objects.get(pk=id)
                u.name = name
                u.email= email
                u.phone = phone
                u.save()
                messages.success(request, "User Updated Succesfull")
            else:    
                MyUSer.objects.create(name=name, email=email, phone=phone)
                messages.success(request, "User Added Successfully")     
        
    return redirect("userform")

def edituser(request, id):
    user = MyUSer.objects.get(pk=id)
    allusers = MyUSer.objects.all()
    return render(request, "userform.html", {"u":user, "users":allusers})

def deleteuser(request, id):
    user = MyUSer.objects.get(pk=id)
    user.delete()
    return redirect("userform")