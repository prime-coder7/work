
from django.contrib import admin
from django.urls import path
from home import views

urlpatterns = [
    path('', views.index, name="index"),
    path('index', views.index, name="index"),
    path('login_user', views.login_user, name="login_user"),
    path('signup_user', views.signup_user, name="signup_user"),
    path('logout', views.logout_user, name="logout"),
    path('forgot_password', views.forgot_password, name="forgot_password"),
    path('profile', views.profile, name="profile"),
    path('society_members', views.society_members, name="society_members"),
    path('society_watchmens', views.society_watchmens, name="society_watchmens"),
    path('notice', views.notice, name="notice"),
    path('events', views.events, name="events"),
]
