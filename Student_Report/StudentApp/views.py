from django.shortcuts import render
from StudentApp.models import *
from django.core.paginator import Paginator

# Create your views here.
def index(request):
    students = Student.objects.all()
    paginator = Paginator(students, 25)  # Show 25 contacts per page.

    page_number = request.GET.get("page")
    page_obj = paginator.get_page(page_number)
    return render(request, "index.html", {"students": page_obj})

def marks(request, id):
    student = Student.objects.get(s_id__student_id=id)  # Fetch the student by student_id
    studentMarks = Mark.objects.filter(student=student)  # Get all marks for the student
        
    return render(request, "marks.html", {'student': student, 'studentMarks': studentMarks})