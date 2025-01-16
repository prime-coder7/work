from django.shortcuts import render, redirect
from django.contrib import messages
from home.models import *

# Create your views here.
def index(request):
    return render(request, "index.html")

def customer(request):
    if request.method == "POST":
        username = request.POST.get('username')
        email = request.POST.get('email')
        age = request.POST.get('age')
        gender = request.POST.get('gender')
        phone = request.POST.get('phone')
        
        if not username or not email or not age or not gender or not phone:
            messages.error(request, "fill all the details")
            return render(request, "customer.html")
        else:
            Customer.objects.create(username=username, email=email, age=age, gender=gender, phone=phone)
            messages.success(request, "data stored successfully")
    return render(request, "customer.html")

def user_form(request):
    allusers = MyUser.objects.all()
    return render(request,"userform.html",{"users":allusers})

def adduser(request):
    if request.method=='POST':
        id=request.POST['id']
        name = request.POST['name']
        email= request.POST['email']
        phone=request.POST['phone']
        
        if(id):
           cuser = MyUser.objects.get(pk=id)
           cuser.name=name
           cuser.email=email
           cuser.phone=phone
           cuser.save()
        else:
            MyUser.objects.create(name=name,email=email,phone=phone)
            
    return redirect("userform")

def deleteuser(request,id):
    user =  MyUser.objects.get(pk=id)
    user.delete()
    return redirect("index")

def edituser(request,id):
    u =  MyUser.objects.get(pk=id)   
    allusers = MyUser.objects.all()
    return render(request,"index.html",{"u":u,"users":allusers})

def login_user(request):
    return render(request, "login.html")

def logout_user(request):
    return render(request, "login.html")

