from django.db import models


class Category(models.Model):
    """
    Rental category
    """
    title = models.CharField(max_length=150, verbose_name='категория')
    description = models.TextField(null=True, blank=True)

    def __str__(self):
        return self.title

    class Meta:
        verbose_name = 'категория'
        verbose_name_plural = 'категории'


class Design(models.Model):
    """
    Rental design
    """
    title = models.CharField(max_length=150, verbose_name='конструкция дома')
    description = models.TextField(null=True, blank=True)

    def __str__(self):
        return self.title

    class Meta:
        verbose_name = 'конструкция'
        verbose_name_plural = 'конструкции'


class Period(models.Model):
    """
    Rental period
    """
    title = models.CharField(max_length=150, verbose_name='период постройки')
    description = models.TextField(null=True, blank=True)

    def __str__(self):
        return self.title

    class Meta:
        verbose_name = 'период постройки'
        verbose_name_plural = 'периоды постройки'


class District(models.Model):
    """
    Rental district
    """
    title = models.CharField(max_length=150, verbose_name='районы')
    description = models.TextField(null=True, blank=True)

    def __str__(self):
        return self.title

    class Meta:
        verbose_name = 'район'
        verbose_name_plural = 'районы'


class Rental(models.Model):
    """
    Model for storing `Rental`
    """
    title = models.CharField(max_length=150, verbose_name='заголовок')

    storeys = models.PositiveIntegerField(default=0, verbose_name='этажность дома')
    bedrooms = models.PositiveIntegerField(default=0, verbose_name='количество комнат')
    total_square = models.PositiveIntegerField(default=0, verbose_name='общая квадратура')
    residential_square = models.PositiveIntegerField(default=0, verbose_name='жилая квадратура')

    city = models.CharField(max_length=150, verbose_name='город')
    description = models.TextField(null=True, verbose_name='описание')

    image = models.ImageField()

    categories = models.ManyToManyField('Category', verbose_name='категории')
    design = models.ManyToManyField('Design', verbose_name='конструкции дома', null=True)

    period = models.ForeignKey('Period', verbose_name='период постройки', null=True, on_delete=models.SET_NULL)
    districts = models.ForeignKey('District', verbose_name='район', null=True, on_delete=models.SET_NULL)

    def __str__(self):
        return self.title

    class Meta:
        verbose_name = 'дом'
        verbose_name_plural = 'дома'
