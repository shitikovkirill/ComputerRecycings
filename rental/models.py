from django.db import models

class Category(models.Model):
    """
    Rental category
    """
    title = models.CharField(max_length=150)
    description = models.TextField(null=True)

class Rental(models.Model):
    """
    Model for storing `Rental`
    """
    title = models.CharField(max_length=150)
    owner = models.CharField(max_length=150)
    city  = models.CharField(max_length=150)
    description = models.TextField(null=True)
    image = models.ImageField()
    category = models.ManyToManyField(Category)
    bedrooms = models.IntegerField()
