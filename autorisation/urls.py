from django.conf.urls import url
from .views import *

user_list = UserViewSet.as_view({
    'get': 'list',
    'post': 'create'
})

group_list = GroupViewSet.as_view({
    'get': 'list',
})

urlpatterns = [
    url(r'^users/$', user_list, name='user-detail'),
    url(r'^groups/$', group_list, name='group-detail'),
]
