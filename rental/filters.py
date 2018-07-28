
class SliderFilter(object):

    def filter_queryset(self, request, queryset, view):
        queryset = self.get_bedrooms_filter(request, queryset)
        queryset = self.get_storey_filter(request, queryset)
        queryset = self.get_total_square_filter(request, queryset)
        queryset = self.get_residential_square_filter(request, queryset)
        return queryset

    @staticmethod
    def get_bedrooms_filter(request, queryset):
        bedrooms = request.GET.get('bedrooms')
        if bedrooms:
            queryset = queryset.filter(bedrooms=bedrooms)
        return queryset

    @staticmethod
    def get_storey_filter(request, queryset):
        storey = request.GET.get('storey')
        if storey:
            queryset = queryset.filter(storeys=storey)
        return queryset

    @staticmethod
    def get_total_square_filter(request, queryset):
        max_total_square = request.GET.get('max_total_square')
        min_total_square = request.GET.get('min_total_square')
        if max_total_square:
            queryset = queryset.filter(total_square__lte=max_total_square)

        if min_total_square:
            queryset = queryset.filter(total_square__gte=min_total_square)
        return queryset

    @staticmethod
    def get_residential_square_filter(request, queryset):
        max_residential_square = request.GET.get('max_residential_square')
        min_residential_square = request.GET.get('min_residential_square')

        if max_residential_square:
            queryset = queryset.filter(residential_square__lte=max_residential_square)

        if min_residential_square:
            queryset = queryset.filter(residential_square__gte=min_residential_square)

        return queryset


class SelectFilter(object):

    def filter_queryset(self, request, queryset, view):
        queryset = self.get_category_filter(request, queryset)
        queryset = self.get_design_filter(request, queryset)
        queryset = self.get_district_filter(request, queryset)
        queryset = self.get_period_filter(request, queryset)

        return queryset

    @staticmethod
    def get_category_filter(request, queryset):
        category = request.GET.get('category')
        if category:
            queryset = queryset.filter(categories__id=category)
        return queryset

    @staticmethod
    def get_design_filter(request, queryset):
        design = request.GET.get('design')
        if design:
            queryset = queryset.filter(design__id=design)
        return queryset

    @staticmethod
    def get_district_filter(request, queryset):
        district = request.GET.get('district')
        if district:
            queryset = queryset.filter(design__id=district)
        return queryset

    @staticmethod
    def get_period_filter(request, queryset):
        period = request.GET.get('period')
        if period:
            queryset = queryset.filter(period__id=period)
        return queryset
