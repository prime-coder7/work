import random
from home.models import *

from faker import Faker
fake = Faker()

def generate_students(n=50):
    dept = Department.objects.all()
    
    for i in range(n):
        d_index = random.randint(0, len(dept)-1)    
        d = dept[d_index]
        fname = fake.first_name()
        lname = fake.last_name()
        age = random.randint(20, 40)
        email = fake.email()
        sid = StudentId.objects.create(student_id = f"S{random.randint(1, 9)}_{random.randint(111, 999)}")
        print(d, sid, email, fname, lname, age)
        
        Student.objects.create(firstName=fname, lastName=lname, age=age, email=email, dept=d, student_id=sid)
        
def generate_marks(n=50):
    students = Student.objects.all()
    
    for s in students:
        subjects = Subject.objects.all()
    
        for sub in subjects:
            m = random.randint(20, 100)
            print(s, sub, m)
            
            Mark.objects.create(student=s, subject=sub, marks=m)
            