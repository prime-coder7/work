# Generated by Django 5.1.4 on 2025-02-22 13:04

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('home', '0004_user_role'),
    ]

    operations = [
        migrations.AlterField(
            model_name='user',
            name='address',
            field=models.TextField(blank=True, null=True),
        ),
    ]
