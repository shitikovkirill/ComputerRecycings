from rest_framework import serializers
from .models import *


class CategorySerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Category
        fields = ('title', 'description', 'id')


class RentalSerializer(serializers.HyperlinkedModelSerializer):
    categories = CategorySerializer(many=True)

    class Meta:
        model = Rental
        fields = ('title', 'owner', 'city', 'description', 'image', 'categories', 'bedrooms', 'id')
