from django.shortcuts import render, HttpResponse
from rest_framework.decorators import api_view
from rest_framework.response import Response
from api.serializer import *
from api.models import *

# Create your views here.

@api_view(['get'])
def getProducts(request):
    return HttpResponse("get called")

@api_view(['post'])
def addProduct(request):
    return HttpResponse("add called")

@api_view(['patch'])
def updateProduct(request):
    return HttpResponse("update called")

@api_view(['delete'])
def deleteProduct(request):
    return HttpResponse("delete called")
