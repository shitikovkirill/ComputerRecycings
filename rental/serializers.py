from rest_framework import serializers
from .models import *


class CategorySerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Category
        fields = ('title', 'description', 'id')


class DesignSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Design
        fields = ('title', 'description', 'id')


class PeriodSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Period
        fields = ('title', 'description', 'id')


class DistrictSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = District
        fields = ('title', 'description', 'id')


class RentalSerializer(serializers.HyperlinkedModelSerializer):
    categories = CategorySerializer(many=True)
    design = DesignSerializer(many=True)
    period = PeriodSerializer()
    districts = DistrictSerializer()

    class Meta:
        model = Rental
        fields = ('title', 'storeys', 'bedrooms', 'total_square', 'residential_square', 'city', 'description',
                  'image', 'categories', 'design', 'period', 'districts', 'id')
