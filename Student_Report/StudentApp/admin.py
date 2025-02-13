from django.contrib import admin
from StudentApp.models import *

# Register your models here.

class DepartmentAdmin(admin.ModelAdmin):
    list_display = ['dept_name']


class StudentIdAdmin(admin.ModelAdmin):
    list_display = ['student_id']
      
class StudentAdmin(admin.ModelAdmin):
    list_display = ['firstname', 'lastname']

class SubjectAdmin(admin.ModelAdmin):
    list_display = ['subject_name']

class MarkAdmin(admin.ModelAdmin):
    list_display = ['student', 'subject']

admin.site.register(Department,DepartmentAdmin)
admin.site.register(StudentId,StudentIdAdmin)
admin.site.register(Student,StudentAdmin)
admin.site.register(Subject,SubjectAdmin)
admin.site.register(Mark,MarkAdmin)