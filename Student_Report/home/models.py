from django.db import models

# Create your models here.
class StudentId(models.Model):
    student_id = models.CharField(max_length=10)

class Department(models.Model):
    deptName = models.CharField(max_length=50)
    
class Student(models.Model):
    dept = models.ForeignKey(Department, on_delete=models.CASCADE)
    student_id = models.OneToOneField(StudentId, on_delete=models.CASCADE)
    firstName = models.CharField(max_length=50)
    lastName = models.CharField(max_length=50)
    age = models.IntegerField()
    email = models.CharField(max_length=50)

class Subject(models.Model):
    subjectName = models.CharField(max_length=50)

class Mark(models.Model):
    student = models.ForeignKey(Student, on_delete=models.CASCADE)
    subject = models.ForeignKey(Subject, on_delete=models.CASCADE)
    marks = models.IntegerField()
    