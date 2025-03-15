from django.shortcuts import render, HttpResponse
from rest_framework.decorators import api_view
from rest_framework.response import Response
from api.models import *
from api.serializer import *


# Create your views here.
@api_view(['get'])
def getStudents(request):
    students = Student.objects.all()
    s_data = StudentSerializer(students, many=True)
    print(s_data)
    return Response(s_data.data)

@api_view(['post'])
def addStudent(request):
    serialize_data = StudentSerializer(data = request.data)
    if not serialize_data.is_valid():
        return Response({'status':'202', 'errors' : serialize_data.errors, 'message' : "Something Went Wrong"})
    serialize_data.save()
    return Response({"status" : '200', 'data' : serialize_data.data, 'message' : "Student Inserted" })

@api_view(['put'])
def updateStudent(request, id):
    try:
        student = Student.objects.get(pk=id)        
        serialize_data = StudentSerializer(student, request.data)
        
        if not serialize_data.is_valid():
            return Response({'status':'202', 'errors' : serialize_data.errors, 'message' : "Something Went Wrong"})
        serialize_data.save()
        
        return Response({"status" : '200', 'data' : serialize_data.data, 'message' : "Student Updated" })
    
    except Exception as e:
        return Response({'status':'404', 'error': e})
    
@api_view(['patch'])
def updateStudent(request, id):
    try:
        student = Student.objects.get(pk=id)        
        serialize_data = StudentSerializer(student, request.data, partial=True)
        
        if not serialize_data.is_valid():
            return Response({'status':'202', 'errors' : serialize_data.errors, 'message' : "Something Went Wrong"})
        serialize_data.save()
        
        return Response({"status" : '200', 'data' : serialize_data.data, 'message' : "Student Updated" })
    
    except Exception as e:
        return Response({'status':'404', 'error': e})
    
    
    

@api_view(['delete'])
def deleteStudent(request, id):
    try:
        student = Student.objects.get(pk=id)
        student.delete()
        return Response({'status':'204', 'message': 'Student deleted successfully'})
    except Exception as e:
        return Response({'status':'404', 'error': e})
    