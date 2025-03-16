from django.shortcuts import render,HttpResponse
from rest_framework.decorators import api_view
from rest_framework.response import Response
from api.serializer import *
from api.models import *
from django.core.exceptions import ObjectDoesNotExist
from rest_framework import status
from django.shortcuts import get_object_or_404



# Create your views here.

# -> CATOGORIES:
@api_view(['GET'])
def getCategories(request):
    categories = Category.objects.all()
    s_categories = CategorySerializer(categories, many=True)
    
    return Response(data = s_categories.data)

@api_view(['POST'])
def addCategory(request):
    try:
        s_category = CategorySerializer(data=request.data)
        if not s_category.is_valid():
            return Response({'status': '400', 'errors': s_category.errors, 'message': 'Invalid data provided'}, status=status.HTTP_400_BAD_REQUEST)
        s_category.save()
        return Response({'status': '201', 'data': s_category.data, 'message': 'Category Added Successfully'}, status=status.HTTP_201_CREATED)
    except Exception as e:
        return Response({'status': '500', 'errors': str(e), 'message': 'Internal Server Error'}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)


@api_view(['PATCH'])
def updateCategory(request, id):
    try:
        category = Category.objects.get(pk=id)
    except ObjectDoesNotExist:
        return Response({'status': '404', 'message': 'Category not found'}, status=status.HTTP_404_NOT_FOUND)

    serializer = CategorySerializer(category, data=request.data, partial=True)
    if serializer.is_valid():
        serializer.save()
        return Response({'status': '200', 'data': serializer.data, 'message': "Category Updated Successfully" }, status=status.HTTP_200_OK)
    else:
        return Response({ 'status': '400', 'errors': serializer.errors, 'message': 'Invalid data' }, status=status.HTTP_400_BAD_REQUEST)

@api_view(['DELETE'])
def deleteCategory(request, id):
    category = get_object_or_404(Category, pk=id)  # This will raise 404 automatically if category does not exist
    category.delete()
    return Response({'status': '200', 'message': "Category Deleted Successfully"}, status=status.HTTP_200_OK)

# -> PRODUCTS:
@api_view(['GET'])
def getProducts(request):
    products = Product.objects.all()
    s_products = ProductSerializer(products, many=True)
    
    return Response(data = s_products.data)

@api_view(['POST'])
def addProduct(request):
    try:
        s_product = ProductSerializer(data=request.data)
        if not s_product.is_valid():
            return Response({'status':'202', 'errors':s_product.errors, 'message':'Something Went Wrong'})
        s_product.save()
        return Response({'status':'200', 'data':s_product.data, 'message':"Product Added Successfully"})
    except Exception as e:
        return Response({'status':'202', 'errors':str(e), 'message':'Something Went Wrong'})

@api_view(['PATCH'])
def updateProduct(request, id):
    try:
        product = Product.objects.get(pk=id)
    except ObjectDoesNotExist:
        return Response({'status': '404', 'message': 'Category not found'}, status=status.HTTP_404_NOT_FOUND)

    serializer = ProductSerializer(product, data=request.data, partial=True)
    if serializer.is_valid():
        serializer.save()
        return Response({'status': '200', 'data': serializer.data, 'message': "Product Updated Successfully" }, status=status.HTTP_200_OK)
    else:
        return Response({ 'status': '400', 'errors': serializer.errors, 'message': 'Invalid data' }, status=status.HTTP_400_BAD_REQUEST)


@api_view(['DELETE'])
def deleteProduct(request, id):
    product = get_object_or_404(Product, pk=id)  
    product.delete()
    return Response({'status': '200', 'message': "Product Deleted Successfully"}, status=status.HTTP_200_OK)