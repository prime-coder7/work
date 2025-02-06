from django.shortcuts import render, HttpResponse
from django.http import JsonResponse
from home.models import *
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
            return HttpResponse("Enter All Details")
        
        else:
            Student.objects.create(username=uname, email=email, phone=phone)
    return HttpResponse("Registration Successfull")


def viewStudent(request):
    allStudents = Student.objects.all()
    return JsonResponse({"allStudents" : list(allStudents.values())})

def edit(request):
    # if request.method == "GET":
        id = request.GET.get("uid")
        st = Student.objects.filter(id=id)
        print(st)
        return JsonResponse({"st" : list(st.values())})
        

def updateStudent(request):
    if request.method == "POST":
        data = request.POST
        id = data.get("id")
        uname = data.get("uname")
        email = data.get("email")
        phone = data.get("phone")
        
        if not uname or not email or not phone:
            return HttpResponse("Field Empty - Try Again")
        
        else:
            if id:
                st = Student.objects.get(pk=id)
                st.username=uname
                st.email=email
                st.phone=phone
                st.save()
    return HttpResponse("Student Updated Successfully")


def deleteStudent(request):
    id = request.GET.get("uid")
    st = Student.objects.get(pk=id)
    st.delete()
    return HttpResponse("Student Deleted Successfully")

def searchStudent(request):
    pass