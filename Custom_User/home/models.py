from django.db import models
from django.contrib.auth.models import AbstractUser
from home.manager import UserManager

# Create your models here.
class CustomUSer(AbstractUser):
    username = None
    
    email = models.CharField(max_length=50, unique=True)
    user_info = models.CharField(max_length=100)
    
    USERNAME_FIELD = "email"
    REQUIRED_FIELDS = []

    
    objects=UserManager()