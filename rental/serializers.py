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
    totalsquare = serializers.IntegerField(source='total_square')
    residentialsquare = serializers.IntegerField(source='residential_square')
    categories = CategorySerializer(many=True)
    design = DesignSerializer(many=True)
    period = PeriodSerializer()
    district = DistrictSerializer()

    class Meta:
        model = Rental
        fields = ('title', 'storeys', 'bedrooms', 'totalsquare', 'residentialsquare', 'description',
                  'image', 'categories', 'design', 'period', 'district', 'id')
