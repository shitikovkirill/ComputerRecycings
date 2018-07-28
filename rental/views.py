from rest_framework import viewsets
from .serializers import *
from .models import *


class RentalViewSet(viewsets.ModelViewSet):
    """
    API endpoint that allows users to be viewed or edited.
    """
    queryset = Rental.objects.all()
    serializer_class = RentalSerializer

    def filter_queryset(self, queryset):

        bedrooms = self.request.GET.get('bedrooms')
        storey = self.request.GET.get('storey')
        max_total_square = self.request.GET.get('max_total_square')
        min_total_square = self.request.GET.get('min_total_square')
        max_residential_square = self.request.GET.get('max_residential_square')
        min_residential_square = self.request.GET.get('min_residential_square')

        if bedrooms:
            queryset = queryset.filter(bedrooms=bedrooms)

        if storey:
            queryset = queryset.filter(storeys=int(storey))

        if max_total_square:
            queryset = queryset.filter(total_square__lte=max_total_square)

        if min_total_square:
            queryset = queryset.filter(total_square__gte=min_total_square)

        if max_residential_square:
            queryset = queryset.filter(residential_square__lte=max_residential_square)

        if min_residential_square:
            queryset = queryset.filter(residential_square__gte=min_residential_square)

        return super().filter_queryset(queryset)


class CategoryViewSet(viewsets.ModelViewSet):
    """
    API endpoint that allows groups to be viewed or edited.
    """
    queryset = Category.objects.all()
    serializer_class = CategorySerializer


class DesignViewSet(viewsets.ModelViewSet):
    """
    API endpoint that allows groups to be viewed or edited.
    """
    queryset = Design.objects.all()
    serializer_class = DesignSerializer


class PeriodViewSet(viewsets.ModelViewSet):
    """
    API endpoint that allows groups to be viewed or edited.
    """
    queryset = Period.objects.all()
    serializer_class = PeriodSerializer


class DistrictViewSet(viewsets.ModelViewSet):
    """
    API endpoint that allows groups to be viewed or edited.
    """
    queryset = District.objects.all()
    serializer_class = DistrictSerializer
