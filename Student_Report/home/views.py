from django.shortcuts import render
from home.models import *
from django.core.paginator import Paginator
from django.db.models import Sum


# Create your views here.
def index(request):
    students = Student.objects.all()
    
    paginator = Paginator(students, 10)  # Show 25 contacts per page.

    page_number = request.GET.get("page")
    page_obj = paginator.get_page(page_number)
    return render(request, "index.html", {"students": page_obj})

def marks(request, id):
    # student = Student.objects.get(student_id__student_id=id)
    # marks = Mark.objects.filter(student=student)
    
    rank = Student.objects.annotate(totalmarks = Sum("mark__marks")).order_by("-totalmarks")
    count = 0
    for i in rank:
        count += 1
        if i.student_id.student_id == id:
            rank_num = count
            break
        studentMarks = Mark.objects.filter(student__student_id__student_id=id)
        student = studentMarks[0].student
        total = studentMarks.aggregate(total=Sum("marks"))
        
    return render(request, 'marks.html', {"student": student, "studentMarks": studentMarks, "total": total, "rank_num": rank_num})