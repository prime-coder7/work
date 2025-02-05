from django.shortcuts import render, HttpResponse
from home.models import *
from django.http import JsonResponse
from django.contrib import messages


# Create your views here.
def index(request):
    return render(request, "index.html")

def addStudent(request):
    if request.method == "POST":
        data = request.POST
        uname = data.get("uname")
        email = data.get("email")
        phone = data.get("phone")
        
        if not uname or not email or not phone:
            messages.error(request, "Fil all the details")
        
        else:
            Student.objects.create(username=uname, email=email, phone=phone)
    return HttpResponse("Registration Successfull")

def viewStudent(request):
    allStudents = Student.objects.all()
    return JsonResponse({"allStudents" : list(allStudents.values())})