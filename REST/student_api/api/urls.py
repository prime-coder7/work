
from django.contrib import admin
from django.urls import path
from api.views import *

urlpatterns = [
    path("getStudents", getStudents, name="getStudents"),
    path("addStudent", addStudent, name="addStudent"),
    path("updateStudent/<id>", updateStudent, name="updateStudent"),
    path("deleteStudent/<id>", deleteStudent, name="deleteStudent"),
]
