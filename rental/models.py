from django.db import models


class Category(models.Model):
    """
    Rental category
    """
    title = models.CharField(max_length=150)
    description = models.TextField(null=True)

    def __str__(self):
        return self.title


class Rental(models.Model):
    """
    Model for storing `Rental`
    """
    title = models.CharField(max_length=150)
    owner = models.CharField(max_length=150)
    city  = models.CharField(max_length=150)
    description = models.TextField(null=True)
    image = models.ImageField()
    categories = models.ManyToManyField('Category')
    bedrooms = models.IntegerField()

    def __str__(self):
        return self.title
