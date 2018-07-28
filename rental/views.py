from rest_framework import viewsets
from .serializers import *
from .models import *
from .filters import *


class RentalViewSet(viewsets.ModelViewSet):
    """
    API endpoint that allows users to be viewed or edited.
    """
    queryset = Rental.objects.all()
    serializer_class = RentalSerializer
    filter_backends = (SliderFilter, SelectFilter)


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
