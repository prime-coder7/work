from django.shortcuts import render
from StudentApp.models import *

# Create your views here.
def index(request):
    students = Student.objects.all()
    return render(request, "index.html", {"students" :students})

def marks(request, id):
    student = Student.objects.get(s_id__student_id=id)  # Fetch the student by student_id
    studentMarks = Mark.objects.filter(student=student)  # Get all marks for the student
        
    return render(request, "marks.html", {'student': student, 'studentMarks': studentMarks})