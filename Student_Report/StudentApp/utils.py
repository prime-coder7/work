from faker import Faker
fake = Faker()
import random
from StudentApp.models import *


def generate_data(n=50):
    dept = Department.objects.all()
   
    for i in range(n):
        d_index = random.randint(0,len(dept)-1)
        d = dept[d_index]
        fname = fake.name().split()[0]
        lname = fake.name().split()[1]
        email=fake.email()
        age  = random.randint(20,40)
        s_id = StudentId.objects.create(student_id =  f'STU_{random.randint(111,999)}')
        Student.objects.create(dept=d, s_id=s_id, firstname=fname, lastname=lname, age=age, email=email)
        
def generate_marks():
    students = Student.objects.all()
    for student in students:
        for subject in Subject.objects.all():
            marks = random.randint(20,100)
            Mark.objects.create(student=student, subject=subject, marks=marks)
