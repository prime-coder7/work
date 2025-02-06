from django.contrib import admin
from django.urls import path
from home import views

urlpatterns = [
    path('', views.index, name="index"),
    path('index', views.index, name="index"),
    path('addStudent', views.addStudent, name="addStudent"),
    path('viewStudent', views.viewStudent, name="viewStudent"),
    path('edit', views.edit, name="edit"),
    path('updateStudent', views.updateStudent, name="updateStudent"),
    path('deleteStudent', views.deleteStudent, name="deleteStudent"),
    path('searchStudent', views.searchStudent, name="searchStudent"),
]
