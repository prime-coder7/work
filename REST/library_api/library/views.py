from django.shortcuts import render, HttpResponse
from rest_framework.decorators import api_view
from rest_framework.response import Response
from rest_framework import status
from library.serializer import *
from library.models import *

# Create your views here.
@api_view(['get'])
def getBooks(request):
    books = Book.objects.all()
    s_books = BookSerializer(books, many=True)
    return Response(s_books.data)

@api_view(['post'])
def addBook(request):
    s_book = BookSerializer(data=request.data)
    
    if not s_book.is_valid():
        return Response({'status':'202', 'errors':s_book.errors, 'message':"Something Went Wrong"})
    s_book.save()
    return Response({'status':'200', 'data':s_book.data, 'message':"Book Added"})

@api_view(['patch'])
def updateBook(request, id):
    try:
        book = Book.objects.get(pk=id)
        s_book = BookSerializer(book, request.data, partial=True)
        if not s_book.is_valid():
            return Response({'status':'202', 'errors':s_book.errors, 'message':"Something Went Wrong"})
        s_book.save()
        return Response({'status':'200',"data":s_book.data,'message':"Book updated"})
    except Exception as e:
        return Response({'status':'202', 'errors':str(e), 'message':"Something Went Wrong"})

@api_view(['DELETE'])
def deleteBook(request, id):
    try:
        book = Book.objects.get(pk=id)
        book_data = BookSerializer(book).data  # Serializing the book before returning
        book.delete()
        return Response({'status': '200', "data": book_data, 'message': "Book Deleted"}, status=status.HTTP_200_OK)
    except Book.DoesNotExist:
        return Response({'status': '404', 'message': "Book not found"}, status=status.HTTP_404_NOT_FOUND)
    except Exception as e:
        return Response({'status': '500', 'errors': str(e), 'message': "Something Went Wrong"}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
