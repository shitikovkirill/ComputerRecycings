from django.contrib import admin
from import_export import resources
from import_export.admin import ImportExportModelAdmin
from .models import *


class CategoryResource(resources.ModelResource):

    class Meta:
        model = Category


class CategoryAdmin(ImportExportModelAdmin, admin.ModelAdmin):
    resource_class = CategoryResource
    list_display = ('title',)
    list_per_page = 20


class DesignResource(resources.ModelResource):

    class Meta:
        model = Design


class DesignAdmin(ImportExportModelAdmin, admin.ModelAdmin):
    resource_class = DesignResource
    list_display = ('title',)
    list_per_page = 20


class PeriodResource(resources.ModelResource):

    class Meta:
        model = Period


class PeriodAdmin(ImportExportModelAdmin, admin.ModelAdmin):
    resource_class = PeriodResource
    list_display = ('title',)
    list_per_page = 20


class DistrictResource(resources.ModelResource):

    class Meta:
        model = District


class DistrictAdmin(ImportExportModelAdmin, admin.ModelAdmin):
    resource_class = DistrictResource
    list_display = ('title',)
    list_per_page = 20


class RentalResource(resources.ModelResource):

    class Meta:
        model = Rental


class RentalAdmin(ImportExportModelAdmin, admin.ModelAdmin):
    resource_class = RentalResource
    list_display = ('title', 'period', 'districts', 'image', )
    list_per_page = 20


admin.site.register(Rental, RentalAdmin)
admin.site.register(Category, CategoryAdmin)
admin.site.register(Design, DesignAdmin)
admin.site.register(Period, PeriodAdmin)
admin.site.register(District, DistrictAdmin)
