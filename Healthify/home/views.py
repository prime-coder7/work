from django.shortcuts import render

# Create your views here.
def index(request):
    return render(request, "index.html")

def doctors(request):
    return render(request, "doctors.html")

def about(request):
    return render(request, "about.html")

def services(request):
    return render(request, "services.html")

def testimonial(request):
    return render(request, "testimonial.html")

def contact(request):
    return render(request, "contact.html")

def appointment(request):
    return render(request, "appointment.html")

def signup_user(request):
    return render(request, "signup.html")

def login_user(request):
    return render(request, "login.html")