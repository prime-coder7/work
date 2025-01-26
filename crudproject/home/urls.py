"""
URL configuration for crudproject project.

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/5.1/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  path('', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  path('', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.urls import include, path
    2. Add a URL to urlpatterns:  path('blog/', include('blog.urls'))
"""
from django.contrib import admin
from django.urls import path, include
from . import views

urlpatterns = [
    path('', views.index, name='index'),
    path('index', views.index, name='index'),
    path('login', views.loginuser, name='login'),
    path('logout', views.logoutuser, name='logout'),
    path('signup', views.signup, name='signup'),
    path('userform', views.userform, name='userform'),
    path('adduser', views.adduser, name='adduser'),
    path('edituser/<id>', views.edituser, name='edituser'),
    path('deleteuser/<id>', views.deleteuser, name='deleteuser'),
]
