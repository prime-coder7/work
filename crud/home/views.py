from django.shortcuts import render, redirect
from django.contrib.auth.models import User
from django.contrib.auth import authenticate, login, logout
from django.contrib import messages
from home.models import *
from django.core.exceptions import ValidationError

# Create your views here.
def index(request):
    if request.user.is_anonymous:
        return redirect("login")
    return render(request, "index.html")

def login_user(request):
    if request.user.is_authenticated:
        return redirect("index")
    
    if request.method == "POST":
        username = request.POST.get('username')
        password = request.POST.get('password')
        
        if not username or not password:
            messages.error(request, "Fill All The Details.")
            return render(request, "login.html")
        else:
            user = authenticate(request, username=username, password=password)
            
            if user:
                login(request, user)
                return redirect("index")
            else:
                messages.error(request, "Enter Correct Details.")
                return render(request, "login.html")
    return render(request, "login.html")

def logout_user(request):
    if request.user.is_authenticated:
        logout(request)
        messages.success(request, "You have been logged out successfully.")

    return redirect("login")

def signup_user(request):
    if request.user.is_authenticated:
        return redirect("index")

    if request.method == "POST":
        uname = request.POST.get('uname', '').strip()
        firstname = request.POST.get('fname', '').strip()
        lastname = request.POST.get('lname', '').strip()
        email = request.POST.get('email', '').strip()
        password = request.POST.get('pass', '').strip()

        if not uname or not firstname or not lastname or not email or not password:
            messages.error(request, "All fields are required.")
            return render(request, "signup.html")

        if User.objects.filter(username=uname).exists():
            messages.error(request, "Username already exists. Please choose another.")
            return render(request, "signup.html")

        try:
            user = User(username=uname, first_name=firstname, last_name=lastname, email=email)
            user.set_password(password)
            user.full_clean() 
            user.save()  
            messages.success(request, "Registration successful! Please log in.")
            return redirect("login")  
        
        except ValidationError as e:
            messages.error(request, f"Error: {e}")
            return render(request, "signup.html")
        
    return render(request, "signup.html")

def userform(request):
    if request.user.is_anonymous:
        return redirect("login")
    
    allusers = MyUser.objects.all()
    return render(request, "userform.html", {"users":allusers})

def adduser(request):
    if request.user.is_anonymous:
        return redirect("login")
    else:
        if request.method == "POST":
            id = request.POST['id']
            username = request.POST['username']
            email = request.POST['email']
            phone = request.POST['phone']
            
            if not username or not email or not phone:
                messages.error(request, "Fill All The Details.")
                return render(request, "userform.html")
            else:
                if id:
                    cuser = MyUser.objects.get(pk=id)
                    cuser.username = username 
                    cuser.email = email 
                    cuser.phone = phone 
                    cuser.save()
                else:
                    MyUser.objects.create(username=username, email=email, phone=phone)
    return redirect("userform")

def edituser(request, id):
    u = MyUser.objects.get(pk=id)
    allusers = MyUser.objects.all()
    return render(request, "userform.html", {"u":u, "users":allusers})

def deleteuser(request, id):
    user = MyUser.objects.get(pk=id)
    user.delete()
    return redirect("userform")
        