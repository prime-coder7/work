from django.contrib import admin
from django.urls import path, include
from home import views
from django.conf import settings
from django.conf.urls.static import static
from django.contrib.auth import views as auth_views

urlpatterns = [
    path('', views.redirect_based_on_role, name='redirect_based_on_role'),
     
    path('admin_dashboard', views.admin_dashboard, name="admin_dashboard"),
    path('login_user', views.login_user, name="login_user"),
    path('signup_user', views.signup_user, name="signup_user"),
    path('logout', views.logout_user, name="logout"), 
    path('pass_change/', views.pass_change, name='pass_change'),
    path('pass_change_done/', views.pass_change_done, name='pass_change_done'),

    path('admin_profile', views.admin_profile, name="admin_profile"),
    path('manage_members', views.manage_members, name="manage_members"),
    path('manage_watchmens', views.manage_watchmens, name="manage_watchmens"),
    path('manage_notice', views.manage_notice, name="manage_notice"),
    path('add_notice', views.add_notice, name='add_notice'),
    path('manage_events', views.manage_events, name="manage_events"),
    path('index', views.index, name="index"),
    
    path('dashboard', views.user_dashboard, name='user_dashboard'),
    path('profile', views.user_profile, name='user_profile'),
    path('events', views.user_events, name='user_events'),
    path('notices', views.user_notices, name='user_notices'),
    path('members', views.user_members, name='user_members'),
    path('watchmen', views.user_watchmen, name='user_watchmen'),
    path('logout', views.logout_view, name='logout'),

    
    
    
]


if settings.DEBUG:
        urlpatterns += static(settings.MEDIA_URL,
                              document_root=settings.MEDIA_ROOT)