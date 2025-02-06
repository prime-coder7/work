from django.shortcuts import render, HttpResponse
from home.models import *
from django.http import JsonResponse
from django.contrib import messages
from django.db.models import Q


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
            return HttpResponse("Enter all details")
        
        else:
            Student.objects.create(username=uname, email=email, phone=phone)
        return HttpResponse("Registration successfull")

def viewStudent(request):
    allStudents = Student.objects.all()
    return JsonResponse({"allStudents" : list(allStudents.values())})

def edit(request):
    id = request.GET.get("uid")
    st = Student.objects.filter(id=id)
    return JsonResponse({"st" : list(st.values())})

def updateStudent(request):
    if request.method == "POST":
        data = request.POST
        id = data.get("id")
        uname = data.get("uname")
        email = data.get("email")
        phone = data.get("phone")
        
        if not uname or not email or not phone:
            return HttpResponse("Enter all details")
        
        else:
            if id:
                print (id)
                st = Student.objects.get(pk=id)
                st.username = uname
                st.email = email
                st.phone = phone
                st.save()       
        return HttpResponse("Student updated successfully")


def deleteStudent(request):
    id = request.GET.get("uid")
    if id:
        st = Student.objects.get(pk=id)
        st.delete()
        return HttpResponse("Student deleted successfully")
    else:
        return HttpResponse("Try Again")
    
def searchStudent(request):
    data = request.GET.get("data")
    result = Student.objects.filter(
        Q(username__startswith=data) |
        Q(email__startswith=data) |
        Q(phone__startswith=data) )
    return JsonResponse({"result" : list(result.values())})