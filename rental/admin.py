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


class RentalResource(resources.ModelResource):

    class Meta:
        model = Rental


class RentalAdmin(ImportExportModelAdmin, admin.ModelAdmin):
    resource_class = RentalResource
    list_display = ('title', 'owner', 'city', 'image', )
    list_per_page = 20


admin.site.register(Category, CategoryAdmin)
admin.site.register(Rental, RentalAdmin)
