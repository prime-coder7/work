from django.shortcuts import render
from datetime import datetime
from django.contrib import messages
from home.models import *

# Create your views here.
def index(request):
    return render(request, 'index.html')
    
def about(request):
    return render(request, 'about.html')

def customer(request):
    if request.method == "POST":
        username = request.POST.get("username")
        email = request.POST.get("email")
        age = request.POST.get("age")
        gender = request.POST.get("gender")
        phone = request.POST.get("phone")
        
        if not username or not email or not age or not gender or not phone:
            messages.error(request, "Fil all the details")
            
        else:
            customer = Customer(username=username, email=email, age=age, gender=gender, phone=phone, date_time = datetime.now())
            customer.save()
            messages.success(request, "Your details added successfully.")
        
    return render(request, 'customer.html')
    
def contact(request):
    if request.method == "POST":
        name = request.POST.get("name")
        email = request.POST.get("email")
        phone = request.POST.get("phone")
        desc = request.POST.get("desc")
        
        if not name or not email or not phone or not desc:
            messages.error(request, "Fil all the details")
        else:
            contact = Contact(name=name, email=email, phone=phone, desc=desc, date_time=datetime.now())
            contact.save()
            messages.success(request, "Your details added successfully.")
        
    return render(request, 'contact.html')

